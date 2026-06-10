import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/campaign.dart';
import '../services/api_service.dart';

final campaignsProvider = FutureProvider<List<Campaign>>((ref) async {
  final list = await ApiService.getCampaigns();
  return list.map((e) => Campaign.fromJson(e as Map<String, dynamic>)).toList();
});
