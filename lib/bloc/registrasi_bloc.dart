import 'dart:convert';
import 'package:tokokita/helpers/api.dart';
import 'package:tokokita/helpers/api_url.dart';
import 'package:tokokita/model/registrasi.dart';

class RegistrasiBloc {
  static Future<Registrasi> registrasi({
    String? nama,
    String? email,
    String? password,
    String? passwordKonfirmasi,
  }) async {
    String apiUrl = ApiUrl.registrasi;
    print('Registrasi URL: $apiUrl');

    var body = {
      "nama": nama,
      "email": email,
      "password": password,
      "password_confirmation": passwordKonfirmasi,
    };

    print('Registrasi Body: $body');

    try {
      var response = await Api().postWithoutToken(apiUrl, body);
      print('Registrasi Response Status: ${response.statusCode}');
      print('Registrasi Response Body: ${response.body}');
      var jsonObj = json.decode(response.body);
      return Registrasi.fromJson(jsonObj);
    } catch (e) {
      print('RegistrasiBloc Error: $e');
      rethrow;
    }
  }
}
