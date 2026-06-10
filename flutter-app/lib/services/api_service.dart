import 'package:dio/dio.dart';

class ApiService {
  static const String baseUrl = 'https://api.roberttalemwa.online';

  static final Dio _dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 15),
    headers: {'Content-Type': 'application/json'},
  ));

  // ── Sermons ─────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getSermons({
    int page = 1,
    String? search,
    String? series,
  }) async {
    final resp = await _dio.get('/api/sermons', queryParameters: {
      'page': page,
      if (search != null) 'search': search,
      if (series != null) 'series': series,
    });
    return resp.data['data'];
  }

  static Future<Map<String, dynamic>> getSermon(int id) async {
    final resp = await _dio.get('/api/sermons/$id');
    return resp.data['data'];
  }

  static Future<List<dynamic>> getSermonSeries() async {
    final resp = await _dio.get('/api/sermons/series');
    return resp.data['data'];
  }

  // ── Radio ────────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getRadioStatus() async {
    final resp = await _dio.get('/api/radio');
    return resp.data['data'];
  }

  static Future<List<dynamic>> getRadioSchedule() async {
    final resp = await _dio.get('/api/radio/schedule');
    return resp.data['data'];
  }

  // ── Live ─────────────────────────────────────────────────────
  static Future<Map<String, dynamic>> getLiveStatus() async {
    final resp = await _dio.get('/api/live');
    return resp.data['data'];
  }

  // ── Events ───────────────────────────────────────────────────
  static Future<List<dynamic>> getEvents() async {
    final resp = await _dio.get('/api/events');
    return resp.data['data'];
  }

  // ── Prayer ───────────────────────────────────────────────────
  static Future<void> submitPrayer(String message, {String? contact}) async {
    await _dio.post('/api/prayer', data: {
      'message': message,
      if (contact != null) 'contact': contact,
    });
  }

  // ── Giving ───────────────────────────────────────────────────
  static Future<Map<String, dynamic>> initiateGiving({
    required double amount,
    required String currency,
    required String givingType,
    String? donorName,
    String? donorEmail,
  }) async {
    final resp = await _dio.post('/api/give/initiate', data: {
      'amount': amount,
      'currency': currency,
      'giving_type': givingType,
      if (donorName != null) 'donor_name': donorName,
      if (donorEmail != null) 'donor_email': donorEmail,
    });
    return resp.data['data'];
  }

  // ── Device token ─────────────────────────────────────────────
  static Future<void> registerDeviceToken(String token, String platform) async {
    await _dio.post('/api/device/register', data: {
      'token': token,
      'platform': platform,
    });
  }
}
