import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../bloc/certificate_bloc.dart';

/// Widget showing storage information
class StorageInfoWidget extends StatelessWidget {
  const StorageInfoWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<CertificateBloc, CertificateState>(
      builder: (context, state) {
        return state.when(
          initial: () => const SizedBox.shrink(),
          loading: () => const Center(
            child: Padding(
              padding: EdgeInsets.all(32.0),
              child: CircularProgressIndicator(),
            ),
          ),
          loadingMore: (_) => const SizedBox.shrink(),
          loadingDetail: () => const SizedBox.shrink(),
          downloading: () => const SizedBox.shrink(),
          searching: () => const SizedBox.shrink(),
          loaded: (_, __) => const SizedBox.shrink(),
          searchResults: (_) => const SizedBox.shrink(),
          detailLoaded: (_) => const SizedBox.shrink(),
          downloaded: (_) => const SizedBox.shrink(),
          pdfUrlLoaded: (_) => const SizedBox.shrink(),
          storageInfoLoaded: (storageInfo) => Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.storage, color: Colors.blue),
                    const SizedBox(width: 8),
                    const Text(
                      'Storage Information',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Total Certificates
                _buildInfoCard(
                  icon: Icons.folder,
                  iconColor: Colors.blue,
                  label: 'Total Certificates',
                  value: '${storageInfo.totalCertificates}',
                  subtitle: 'PDF files',
                ),

                const SizedBox(height: 12),

                // Total Storage
                _buildInfoCard(
                  icon: Icons.storage,
                  iconColor: Colors.purple,
                  label: 'Total Storage Used',
                  value: storageInfo.totalSizeFormatted,
                  subtitle: 'Disk space',
                ),

                const SizedBox(height: 12),

                // Compressed Count
                _buildInfoCard(
                  icon: Icons.compress,
                  iconColor: Colors.green,
                  label: 'Compressed PDFs',
                  value: '${storageInfo.compressedCount}',
                  subtitle:
                      '${storageInfo.compressionRatio.toStringAsFixed(1)}% of total',
                ),

                const SizedBox(height: 24),

                // Close Button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Close'),
                  ),
                ),
              ],
            ),
          ),
          error: (message) => Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                const SizedBox(height: 16),
                Text(
                  message,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.red),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Close'),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildInfoCard({
    required IconData icon,
    required Color iconColor,
    required String label,
    required String value,
    required String subtitle,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: iconColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: iconColor, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(
                    fontSize: 12,
                    color: Colors.grey,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Text(
                  subtitle,
                  style: const TextStyle(
                    fontSize: 11,
                    color: Colors.grey,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
