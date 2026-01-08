import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';

import '../../domain/entities/certificate.dart';
import '../bloc/certificate_bloc.dart';

/// Page showing certificate detail
class CertificateDetailPage extends StatelessWidget {
  const CertificateDetailPage({
    super.key,
    required this.certificateId,
  });

  final String certificateId;

  @override
  Widget build(BuildContext context) {
    // Load certificate detail on init
    context.read<CertificateBloc>().add(
          CertificateEvent.loadCertificateDetail(id: certificateId),
        );

    return Scaffold(
      appBar: AppBar(
        title: const Text('Certificate Detail'),
        actions: [
          BlocBuilder<CertificateBloc, CertificateState>(
            builder: (context, state) {
              return state.maybeWhen(
                detailLoaded: (certificate) => PopupMenuButton(
                  itemBuilder: (context) => [
                    PopupMenuItem(
                      child: const Row(
                        children: [
                          Icon(Icons.download),
                          SizedBox(width: 8),
                          Text('Download PDF'),
                        ],
                      ),
                      onTap: () {
                        context.read<CertificateBloc>().add(
                              CertificateEvent.downloadCertificate(
                                id: certificate.id,
                              ),
                            );
                      },
                    ),
                    PopupMenuItem(
                      child: const Row(
                        children: [
                          Icon(Icons.share),
                          SizedBox(width: 8),
                          Text('Share'),
                        ],
                      ),
                      onTap: () {
                        // TODO: Implement share functionality
                      },
                    ),
                  ],
                ),
                orElse: () => const SizedBox.shrink(),
              );
            },
          ),
        ],
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
            initial: () => const SizedBox.shrink(),
            loading: () => const Center(child: CircularProgressIndicator()),
            loadingMore: (_) => const SizedBox.shrink(),
            loadingDetail: () => const Center(
              child: CircularProgressIndicator(),
            ),
            downloading: () => const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircularProgressIndicator(),
                  SizedBox(height: 16),
                  Text('Downloading certificate...'),
                ],
              ),
            ),
            searching: () => const SizedBox.shrink(),
            loaded: (_, __) => const SizedBox.shrink(),
            searchResults: (_) => const SizedBox.shrink(),
            detailLoaded: (certificate) => _buildDetail(context, certificate),
            downloaded: (_) => const SizedBox.shrink(),
            pdfUrlLoaded: (_) => const SizedBox.shrink(),
            storageInfoLoaded: (_) => const SizedBox.shrink(),
            error: (message) => Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 64, color: Colors.red),
                  const SizedBox(height: 16),
                  Text(message, textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Go Back'),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildDetail(BuildContext context, Certificate certificate) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Certificate Code Card
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  const Icon(Icons.qr_code, size: 48, color: Colors.blue),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Certificate Code',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          certificate.uniqueCode,
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          const SizedBox(height: 16),

          // Patient Information
          _buildSection(
            'Patient Information',
            Icons.person,
            [
              _buildInfoRow('Name', certificate.patient.name),
              if (certificate.patient.nik != null)
                _buildInfoRow('NIK', certificate.patient.nik!),
              if (certificate.patient.gender != null)
                _buildInfoRow('Gender', certificate.patient.gender!),
              if (certificate.patient.birthdate != null)
                _buildInfoRow('Birth Date', certificate.patient.birthdate!),
              if (certificate.patient.age != null)
                _buildInfoRow('Age', '${certificate.patient.age} years'),
              if (certificate.patient.company != null)
                _buildInfoRow('Company', certificate.patient.company!),
            ],
          ),

          const SizedBox(height: 16),

          // Certificate Information
          _buildSection(
            'Certificate Information',
            Icons.description,
            [
              _buildInfoRow('Type', certificate.certificate.type),
              _buildInfoRow('Status', certificate.certificate.status),
              if (certificate.certificate.result != null)
                _buildInfoRow('Result', certificate.certificate.result!),
              if (certificate.certificate.createdAt != null)
                _buildInfoRow(
                  'Created At',
                  _formatDate(certificate.certificate.createdAt!),
                ),
              if (certificate.doctor?.name != null)
                _buildInfoRow('Doctor', certificate.doctor!.name!),
            ],
          ),

          const SizedBox(height: 16),

          // PDF Information
          _buildSection(
            'PDF Information',
            Icons.picture_as_pdf,
            [
              _buildInfoRow('File Size', certificate.pdf.sizeFormatted),
              _buildInfoRow(
                'Compressed',
                certificate.pdf.isCompressed ? 'Yes' : 'No',
              ),
              if (certificate.pdf.isCompressed &&
                  certificate.pdf.compression != null)
                _buildInfoRow(
                  'Compression Ratio',
                  '${certificate.pdf.compression!.ratio}%',
                ),
              if (certificate.pdf.isCompressed &&
                  certificate.pdf.compression != null)
                _buildInfoRow(
                  'Space Saved',
                  certificate.pdf.compression!.savingsFormatted,
                ),
              if (certificate.pdf.generatedAt != null)
                _buildInfoRow(
                  'Generated At',
                  _formatDate(certificate.pdf.generatedAt!),
                ),
            ],
          ),

          const SizedBox(height: 24),

          // Action Buttons
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () {
                context.read<CertificateBloc>().add(
                      CertificateEvent.downloadCertificate(
                        id: certificate.id,
                      ),
                    );
              },
              icon: const Icon(Icons.download),
              label: const Text('Download Certificate'),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.all(16),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection(String title, IconData icon, List<Widget> children) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: Colors.blue),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const Divider(),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: const TextStyle(
                color: Colors.grey,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  String _formatDate(DateTime date) {
    return DateFormat('dd MMM yyyy, HH:mm').format(date);
  }
}
