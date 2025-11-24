import 'dart:convert';

import 'package:tokokita/helpers/api.dart';
import 'package:tokokita/helpers/api_url.dart';
import 'package:tokokita/model/login.dart';

class LoginBloc {
  static Future<Login> login({String? email, String? password}) async {
    try {
      String apiUrl = ApiUrl.login;
      var body = {"email": email, "password": password};
      var response = await Api().postWithoutToken(apiUrl, body);
      var jsonObj = json.decode(response.body);
      return Login.fromJson(jsonObj);
    } catch (e) {
      throw Exception("Login failed: $e");
    }
  }
}
