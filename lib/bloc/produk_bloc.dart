import 'dart:convert';

import 'package:tokokita/helpers/api.dart';
import 'package:tokokita/helpers/api_url.dart';
import 'package:tokokita/model/produk.dart';

class ProdukBloc {
  static Future<List<Produk>> getProduks() async {
    try {
      String apiUrl = ApiUrl.listProduk;
      var response = await Api().get(apiUrl);
      var jsonObj = json.decode(response.body);
      List<dynamic> listProduk = (jsonObj as Map<String, dynamic>)['data'];
      List<Produk> produks = [];
      for (int i = 0; i < listProduk.length; i++) {
        produks.add(Produk.fromJson(listProduk[i]));
      }
      return produks;
    } catch (e) {
      throw Exception("Failed to get produks: $e");
    }
  }

  static Future<Map<String, dynamic>> addProduk({Produk? produk}) async {
    try {
      String apiUrl = ApiUrl.createProduk;
      var body = {
        "kode_produk": produk!.kodeProduk,
        "nama_produk": produk.namaProduk,
        "harga": produk.hargaProduk,
      };
      var response = await Api().post(apiUrl, body);
      var jsonObj = json.decode(response.body);
      return jsonObj;
    } catch (e) {
      throw Exception("Failed to add produk: $e");
    }
  }

  static Future<Map<String, dynamic>> updateProduk({
    required Produk produk,
  }) async {
    try {
      String apiUrl = ApiUrl.updateProduk(produk.id!);
      var body = {
        "kode_produk": produk.kodeProduk,
        "nama_produk": produk.namaProduk,
        "harga": produk.hargaProduk,
      };
      var response = await Api().put(apiUrl, body);
      var jsonObj = json.decode(response.body);
      return jsonObj;
    } catch (e) {
      throw Exception("Failed to update produk: $e");
    }
  }

  static Future<bool> deleteProduk({int? id}) async {
    try {
      String apiUrl = ApiUrl.deleteProduk(id!);

      // LOG UNTUK DEBUGGING
      print("--- DELETING PRODUCT ---");
      print("--- URL CALLED: $apiUrl ---");

      var response = await Api().delete(apiUrl);
      var jsonObj = json.decode(response.body);

      print("--- SERVER RESPONSE: $jsonObj ---");

      return jsonObj['status'];
    } catch (e) {
      // LOG JIKA TERJADI ERROR
      print("--- DELETE FAILED ---");
      print("--- ERROR: $e ---");
      rethrow;
    }
  }
}
