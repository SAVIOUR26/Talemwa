import 'package:flutter/material.dart';
import '../../core/theme.dart';
import '../../services/api_service.dart';

class PrayerScreen extends StatefulWidget {
  const PrayerScreen({super.key});
  @override State<PrayerScreen> createState() => _PrayerScreenState();
}

class _PrayerScreenState extends State<PrayerScreen> {
  final _msgCtrl     = TextEditingController();
  final _contactCtrl = TextEditingController();
  bool   _loading    = false;
  bool   _sent       = false;
  String? _error;

  @override
  void dispose() { _msgCtrl.dispose(); _contactCtrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('Prayer Request', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _sent ? _SuccessBody() : _FormBody(
        msgCtrl:     _msgCtrl,
        contactCtrl: _contactCtrl,
        loading:     _loading,
        error:       _error,
        onSubmit:    _submit,
      ),
    );
  }

  Future<void> _submit() async {
    if (_msgCtrl.text.trim().isEmpty) {
      setState(() => _error = 'Please enter your prayer request.');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      await ApiService.submitPrayer(
        _msgCtrl.text.trim(),
        contact: _contactCtrl.text.trim().isNotEmpty ? _contactCtrl.text.trim() : null,
      );
      setState(() { _loading = false; _sent = true; });
    } catch (e) {
      setState(() { _loading = false; _error = 'Failed to send. Please try again.'; });
    }
  }
}

class _FormBody extends StatelessWidget {
  final TextEditingController msgCtrl;
  final TextEditingController contactCtrl;
  final bool        loading;
  final String?     error;
  final VoidCallback onSubmit;

  const _FormBody({
    required this.msgCtrl, required this.contactCtrl,
    required this.loading, required this.error, required this.onSubmit,
  });

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.navy,
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Text(
              '"Do not be anxious about anything, but in every situation, by prayer and petition, with thanksgiving, present your requests to God." — Philippians 4:6',
              style: TextStyle(color: Colors.white70, fontSize: 13, fontStyle: FontStyle.italic),
            ),
          ),
          const SizedBox(height: 24),
          const Text('Your Prayer Request', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.navy)),
          const SizedBox(height: 8),
          TextField(
            controller: msgCtrl,
            maxLines:   6,
            decoration: const InputDecoration(
              hintText: 'Share your prayer request with us…',
              border: OutlineInputBorder(),
              contentPadding: EdgeInsets.all(12),
            ),
          ),
          const SizedBox(height: 16),
          const Text('Contact (optional)', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.navy)),
          const SizedBox(height: 8),
          TextField(
            controller: contactCtrl,
            decoration: const InputDecoration(
              hintText: 'Phone or email so we can follow up',
              prefixIcon: Icon(Icons.contact_phone_outlined),
              border: OutlineInputBorder(),
              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
          ),
          const SizedBox(height: 20),
          if (error != null)
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Text(error!, style: const TextStyle(color: Colors.red)),
            ),
          SizedBox(
            width: double.infinity,
            height: 52,
            child: ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.navy,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onPressed: loading ? null : onSubmit,
              icon: loading
                  ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Icon(Icons.send, color: Colors.white),
              label: const Text('Send Prayer Request', style: TextStyle(color: Colors.white, fontSize: 15)),
            ),
          ),
        ],
      ),
    );
  }
}

class _SuccessBody extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(color: AppColors.gold, shape: BoxShape.circle),
              child: const Icon(Icons.check, color: Colors.white, size: 48),
            ),
            const SizedBox(height: 24),
            const Text('Prayer Received', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.navy)),
            const SizedBox(height: 12),
            Text('Pastor Robert and our prayer team will lift you up in prayer.',
                style: TextStyle(color: Colors.grey[600], fontSize: 15),
                textAlign: TextAlign.center),
            const SizedBox(height: 32),
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('Back', style: TextStyle(color: AppColors.navy)),
            ),
          ],
        ),
      ),
    );
  }
}
