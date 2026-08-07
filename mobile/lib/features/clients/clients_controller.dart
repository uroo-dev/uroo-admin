import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/client.dart';
import '../../data/repositories/client_repository.dart';

class ClientsController extends AsyncNotifier<List<Client>> {
  final _repo = ClientRepository();

  @override
  Future<List<Client>> build() => _repo.list();

  Future<void> create(Client client) async {
    await _repo.create(client);
    ref.invalidateSelf();
  }

  Future<void> updateItem(int id, Client client) async {
    await _repo.update(id, client);
    ref.invalidateSelf();
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    ref.invalidateSelf();
  }
}

final clientsProvider =
    AsyncNotifierProvider<ClientsController, List<Client>>(ClientsController.new);