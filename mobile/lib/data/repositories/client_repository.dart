import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/client.dart';

class ClientRepository {
  Future<List<Client>> list() async {
    final res = await supabase
        .from('clients')
        .select()
        .order('created_at', ascending: false);
    return res.map((e) => Client.fromJson(e)).toList();
  }

  Future<Client> create(Client client) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('clients')
        .insert({...client.toMap(), 'user_id': uid})
        .select()
        .single();
    return Client.fromJson(res);
  }

  Future<void> update(int id, Client client) async {
    await supabase.from('clients').update(client.toMap()).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('clients').delete().eq('id', id);
  }
}