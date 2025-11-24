import 'package:flutter/foundation.dart';

class ApiUrl {
  static const String _baseUrlMobile = 'http://10.0.2.2/toko-api';
  static const String _baseUrlWeb = 'http://localhost/toko-api';

  static String get baseUrl {
    if (kIsWeb) {
      return _baseUrlWeb;
    } else {
      return _baseUrlMobile;
    }
  }

  static String get registrasi => baseUrl + '/registrasi.php';
  static String get login => baseUrl + '/login.php';

  static String get listProduk => baseUrl + '/produk.php';
  static String get createProduk => baseUrl + '/produk.php';

  // Menggunakan query parameter `?id=` untuk mengirim ID
  static String updateProduk(int id) {
    return baseUrl + '/produk.php?id=' + id.toString();
  }

  static String showProduk(int id) {
    return baseUrl + '/produk.php?id=' + id.toString();
  }

  static String deleteProduk(int id) {
    return baseUrl + '/produk.php?id=' + id.toString();
  }
}
