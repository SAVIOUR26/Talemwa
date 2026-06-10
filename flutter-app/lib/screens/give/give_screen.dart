import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../../core/theme.dart';
import '../../providers/campaign_provider.dart';
import '../../services/api_service.dart';
import '../../models/campaign.dart';

class GiveScreen extends ConsumerStatefulWidget {
  const GiveScreen({super.key});
  @override ConsumerState<GiveScreen> createState() => _GiveScreenState();
}

class _GiveScreenState extends ConsumerState<GiveScreen> {
  final _types    = ['tithe', 'offering', 'project', 'campaign'];
  final _typeLbls = ['Tithe', 'Offering', 'Project', 'Campaign'];
  final _currencies = ['UGX', 'USD', 'GBP', 'EUR'];
  final _quickAmounts = {
    'UGX': [10000, 50000, 100000, 500000],
    'USD': [5, 10, 25, 50],
    'GBP': [5, 10, 20, 50],
    'EUR': [5, 10, 20, 50],
  };

  int    _typeIdx    = 0;
  int    _currIdx    = 0;
  int?   _campaignId;
  String _amount     = '';
  String _name       = '';
  String _email      = '';
  bool   _loading    = false;
  String? _error;

  final _scriptures = [
    '"Give, and it shall be given unto you." — Luke 6:38',
    '"Each of you should give what you have decided in your heart." — 2 Cor 9:7',
    '"Bring the whole tithe into the storehouse." — Malachi 3:10',
  ];
  int _scriptureIdx = 0;

  @override
  Widget build(BuildContext context) {
    final campaigns = ref.watch(campaignsProvider);

    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('Give', style: TextStyle(color: Colors.white)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Scripture card
            GestureDetector(
              onTap: () => setState(() => _scriptureIdx = (_scriptureIdx + 1) % _scriptures.length),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [AppColors.navy, AppColors.navyLight]),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(_scriptures[_scriptureIdx],
                    style: const TextStyle(color: Colors.white, fontSize: 14, fontStyle: FontStyle.italic),
                    textAlign: TextAlign.center),
              ),
            ),
            const SizedBox(height: 20),

            // Giving type tabs
            const Text('Giving Type', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.navy)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: List.generate(_types.length, (i) => ChoiceChip(
                label: Text(_typeLbls[i]),
                selected: _typeIdx == i,
                onSelected: (_) => setState(() { _typeIdx = i; _campaignId = null; }),
                selectedColor: AppColors.gold,
                labelStyle: TextStyle(color: _typeIdx == i ? Colors.white : AppColors.navy),
              )),
            ),
            const SizedBox(height: 16),

            // Campaign picker
            if (_typeIdx == 3)
              campaigns.when(
                loading: () => const LinearProgressIndicator(),
                error:   (_, __) => const SizedBox(),
                data:    (list) => Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Select Campaign', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.navy)),
                    const SizedBox(height: 8),
                    ...list.map((c) => _CampaignTile(
                      campaign: c,
                      selected: _campaignId == c.id,
                      onTap: () => setState(() => _campaignId = _campaignId == c.id ? null : c.id),
                    )),
                    const SizedBox(height: 16),
                  ],
                ),
              ),

            // Currency selector
            const Text('Currency', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.navy)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: List.generate(_currencies.length, (i) => ChoiceChip(
                label: Text(_currencies[i]),
                selected: _currIdx == i,
                onSelected: (_) => setState(() { _currIdx = i; _amount = ''; }),
                selectedColor: AppColors.navy,
                labelStyle: TextStyle(color: _currIdx == i ? Colors.white : AppColors.navy),
              )),
            ),
            const SizedBox(height: 16),

            // Quick amounts
            const Text('Amount', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.navy)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8, runSpacing: 8,
              children: (_quickAmounts[_currencies[_currIdx]] ?? []).map((a) => GestureDetector(
                onTap: () => setState(() => _amount = a.toString()),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    color: _amount == a.toString() ? AppColors.gold : Colors.white,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: _amount == a.toString() ? AppColors.gold : Colors.grey.shade300),
                  ),
                  child: Text('${_currencies[_currIdx]} $a',
                      style: TextStyle(color: _amount == a.toString() ? Colors.white : AppColors.navy,
                          fontWeight: FontWeight.w600, fontSize: 13)),
                ),
              )).toList(),
            ),
            const SizedBox(height: 12),
            TextField(
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                hintText: 'Or enter custom amount',
                prefixIcon: Icon(Icons.attach_money),
                border: OutlineInputBorder(),
                contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              ),
              onChanged: (v) => setState(() => _amount = v),
              controller: TextEditingController(text: _amount)..selection =
                  TextSelection.collapsed(offset: _amount.length),
            ),
            const SizedBox(height: 16),

            // Optional donor info
            TextField(
              decoration: const InputDecoration(
                hintText: 'Your name (optional)',
                prefixIcon: Icon(Icons.person_outline),
                border: OutlineInputBorder(),
                contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              ),
              onChanged: (v) => _name = v,
            ),
            const SizedBox(height: 10),
            TextField(
              decoration: const InputDecoration(
                hintText: 'Email for receipt (optional)',
                prefixIcon: Icon(Icons.email_outlined),
                border: OutlineInputBorder(),
                contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              ),
              keyboardType: TextInputType.emailAddress,
              onChanged: (v) => _email = v,
            ),
            const SizedBox(height: 20),

            if (_error != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Text(_error!, style: const TextStyle(color: Colors.red)),
              ),

            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.gold,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: _loading ? null : _initiate,
                child: _loading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Give Now', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }

  Future<void> _initiate() async {
    final amt = double.tryParse(_amount);
    if (amt == null || amt <= 0) {
      setState(() => _error = 'Please enter a valid amount.');
      return;
    }

    setState(() { _loading = true; _error = null; });

    try {
      final data = await ApiService.initiateGiving(
        amount:     amt,
        currency:   _currencies[_currIdx],
        givingType: _types[_typeIdx],
        donorName:  _name.isNotEmpty  ? _name  : null,
        donorEmail: _email.isNotEmpty ? _email : null,
        campaignId: _campaignId,
      );

      final link = data['payment_link'] as String?;
      if (link != null && mounted) {
        Navigator.of(context).push(MaterialPageRoute(
          builder: (_) => _PaymentWebView(url: link),
        ));
      }
    } catch (e) {
      setState(() => _error = 'Could not initiate payment. Please try again.');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }
}

class _CampaignTile extends StatelessWidget {
  final Campaign campaign;
  final bool selected;
  final VoidCallback onTap;
  const _CampaignTile({required this.campaign, required this.selected, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: selected ? AppColors.gold.withOpacity(0.1) : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: selected ? AppColors.gold : Colors.grey.shade200),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(campaign.title, style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.navy)),
            const SizedBox(height: 6),
            LinearProgressIndicator(
              value: campaign.progressPercent,
              backgroundColor: Colors.grey.shade200,
              valueColor: const AlwaysStoppedAnimation(AppColors.gold),
            ),
            const SizedBox(height: 4),
            Text(
              '${campaign.currency} ${campaign.raisedAmount.toStringAsFixed(0)} / ${campaign.goalAmount.toStringAsFixed(0)}',
              style: TextStyle(color: Colors.grey[600], fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }
}

class _PaymentWebView extends StatelessWidget {
  final String url;
  const _PaymentWebView({required this.url});

  @override
  Widget build(BuildContext context) {
    final ctrl = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..loadRequest(Uri.parse(url));

    return Scaffold(
      appBar: AppBar(
        title: const Text('Secure Payment'),
        backgroundColor: AppColors.navy,
        foregroundColor: Colors.white,
      ),
      body: WebViewWidget(controller: ctrl),
    );
  }
}
