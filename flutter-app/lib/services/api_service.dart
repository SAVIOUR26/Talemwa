import 'package:dio/dio.dart';
import '../core/constants.dart';

class ApiService {
  static final Dio _dio = Dio(BaseOptions(
    baseUrl:        AppConstants.apiBaseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 15),
    headers:        {'Content-Type': 'application/json'},
  ));

  static Map<String, dynamic> _data(Response r) => r.data['data'] as Map<String, dynamic>;
  static List<dynamic>        _list(Response r) => r.data['data'] as List<dynamic>;

  // ── Sermons ──────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getSermons({int page = 1, String? search, String? series}) async {
    final r = await _dio.get('/api/sermons', queryParameters: {
      'page': page,
      if (search != null && search.isNotEmpty) 'search': search,
      if (series != null && series.isNotEmpty) 'series': series,
    });
    return _data(r);
  }

  static Future<Map<String, dynamic>> getSermon(int id) async {
    final r = await _dio.get('/api/sermons/$id');
    return _data(r);
  }

  static Future<List<dynamic>> getSermonSeries() async {
    final r = await _dio.get('/api/sermons/series');
    return _list(r);
  }

  // ── Radio ────────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getRadioStatus() async {
    final r = await _dio.get('/api/radio');
    return _data(r);
  }

  static Future<List<dynamic>> getRadioSchedule() async {
    final r = await _dio.get('/api/radio/schedule');
    return _list(r);
  }

  // ── Live ─────────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getLiveStatus() async {
    final r = await _dio.get('/api/live');
    return _data(r);
  }

  // ── Events ───────────────────────────────────────────────────
  static Future<List<dynamic>> getEvents() async {
    final r = await _dio.get('/api/events');
    return _list(r);
  }

  static Future<Map<String, dynamic>> getEvent(int id) async {
    final r = await _dio.get('/api/events/$id');
    return _data(r);
  }

  // ── Campaigns ────────────────────────────────────────────────
  static Future<List<dynamic>> getCampaigns() async {
    final r = await _dio.get('/api/campaigns');
    return _list(r);
  }

  // ── Prayer ───────────────────────────────────────────────────
  static Future<void> submitPrayer(String message, {String? contact}) async {
    await _dio.post('/api/prayer', data: {
      'message': message,
      if (contact != null && contact.isNotEmpty) 'contact': contact,
    });
  }

  // ── Giving ───────────────────────────────────────────────────
  static Future<Map<String, dynamic>> initiateGiving({
    required double amount,
    required String currency,
    required String givingType,
    String?  donorName,
    String?  donorEmail,
    int?     campaignId,
  }) async {
    final r = await _dio.post('/api/give/initiate', data: {
      'amount':      amount,
      'currency':    currency,
      'giving_type': givingType,
      if (donorName  != null) 'donor_name':  donorName,
      if (donorEmail != null) 'donor_email': donorEmail,
      if (campaignId != null) 'campaign_id': campaignId,
    });
    return _data(r);
  }

  // ── Device ───────────────────────────────────────────────────
  static Future<void> registerDeviceToken(String token, String platform, {String? country}) async {
    await _dio.post('/api/device/register', data: {
      'token':    token,
      'platform': platform,
      if (country != null) 'country': country,
    });
  }

  static Future<void> trackInstall(String platform, {String? country, String? appVersion}) async {
    await _dio.post('/api/install/track', data: {
      'platform':    platform,
      if (country    != null) 'country':     country,
      if (appVersion != null) 'app_version': appVersion,
    });
  }
}
