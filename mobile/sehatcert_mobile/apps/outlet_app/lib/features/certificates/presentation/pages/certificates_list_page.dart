import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../bloc/certificate_bloc.dart';
import '../widgets/certificate_card.dart';
import '../widgets/storage_info_widget.dart';

/// Page showing list of certificates
class CertificatesListPage extends StatefulWidget {
  const CertificatesListPage({super.key});

  @override
  State<CertificatesListPage> createState() => _CertificatesListPageState();
}

class _CertificatesListPageState extends State<CertificatesListPage> {
  final _scrollController = ScrollController();
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    // Load certificates on init
    context.read<CertificateBloc>().add(
          const CertificateEvent.loadCertificates(),
        );

    // Setup scroll listener for pagination
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_isBottom) {
      context.read<CertificateBloc>().add(
            const CertificateEvent.loadMoreCertificates(),
          );
    }
  }

  bool get _isBottom {
    if (!_scrollController.hasClients) return false;
    final maxScroll = _scrollController.position.maxScrollExtent;
    final currentScroll = _scrollController.offset;
    return currentScroll >= (maxScroll * 0.9);
  }

  void _onSearch(String query) {
    if (query.isEmpty) {
      context.read<CertificateBloc>().add(
            const CertificateEvent.loadCertificates(),
          );
    } else {
      context.read<CertificateBloc>().add(
            CertificateEvent.searchCertificates(query: query),
          );
    }
  }

  Future<void> _onRefresh() async {
    context.read<CertificateBloc>().add(
          const CertificateEvent.refreshCertificates(),
        );
    // Wait a bit for the refresh to complete
    await Future.delayed(const Duration(seconds: 1));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Certificates'),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(60),
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search by code, name, or NIK...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          _onSearch('');
                        },
                      )
                    : null,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
                filled: true,
                fillColor: Colors.white,
              ),
              onChanged: _onSearch,
            ),
          ),
        ),
      ),
      body: BlocConsumer<CertificateBloc, CertificateState>(
        listener: (context, state) {
          state.whenOrNull(
            error: (message) {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(message),
                  backgroundColor: Colors.red,
                ),
              );
            },
            downloaded: (filePath) {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text('Certificate downloaded to: $filePath'),
                  backgroundColor: Colors.green,
                ),
              );
            },
          );
        },
        builder: (context, state) {
          return state.when(
            initial: () => const Center(
              child: Text('No certificates loaded'),
            ),
            loading: () => const Center(
              child: CircularProgressIndicator(),
            ),
            loadingMore: (certificates) => _buildCertificatesList(
              certificates,
              isLoadingMore: true,
            ),
            loadingDetail: () => const Center(
              child: CircularProgressIndicator(),
            ),
            downloading: () => const Center(
              child: CircularProgressIndicator(),
            ),
            searching: () => const Center(
              child: CircularProgressIndicator(),
            ),
            loaded: (certificates, hasMore) => _buildCertificatesList(
              certificates,
              hasMore: hasMore,
            ),
            searchResults: (certificates) => _buildCertificatesList(
              certificates,
              isSearchResult: true,
            ),
            detailLoaded: (_) => const SizedBox.shrink(),
            downloaded: (_) => const SizedBox.shrink(),
            pdfUrlLoaded: (_) => const SizedBox.shrink(),
            storageInfoLoaded: (_) => const SizedBox.shrink(),
            error: (message) => Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.error_outline,
                    size: 64,
                    color: Colors.red,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    message,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      context.read<CertificateBloc>().add(
                            const CertificateEvent.loadCertificates(),
                          );
                    },
                    child: const Text('Retry'),
                  ),
                ],
              ),
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          showModalBottomSheet(
            context: context,
            builder: (_) => BlocProvider.value(
              value: context.read<CertificateBloc>()
                ..add(const CertificateEvent.loadStorageInfo()),
              child: const StorageInfoWidget(),
            ),
          );
        },
        child: const Icon(Icons.storage),
      ),
    );
  }

  Widget _buildCertificatesList(
    List certificates, {
    bool hasMore = false,
    bool isLoadingMore = false,
    bool isSearchResult = false,
  }) {
    if (certificates.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.folder_open,
              size: 64,
              color: Colors.grey,
            ),
            const SizedBox(height: 16),
            Text(
              isSearchResult ? 'No certificates found' : 'No certificates',
              style: const TextStyle(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _onRefresh,
      child: ListView.builder(
        controller: _scrollController,
        padding: const EdgeInsets.all(8),
        itemCount: certificates.length + (isLoadingMore ? 1 : 0),
        itemBuilder: (context, index) {
          if (index >= certificates.length) {
            return const Center(
              child: Padding(
                padding: EdgeInsets.all(16.0),
                child: CircularProgressIndicator(),
              ),
            );
          }

          final certificate = certificates[index];
          return CertificateCard(
            certificate: certificate,
            onTap: () {
              Navigator.pushNamed(
                context,
                '/certificates/detail',
                arguments: certificate.id,
              );
            },
            onDownload: () {
              context.read<CertificateBloc>().add(
                    CertificateEvent.downloadCertificate(id: certificate.id),
                  );
            },
          );
        },
      ),
    );
  }
}
