import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/brain_dump.dart';

class BrainDumpRepository {
  Future<List<BrainDump>> list({bool includeArchived = false}) async {
    final builder = supabase.from('brain_dumps').select();
    final query = !includeArchived ? builder.eq('is_archived', false) : builder;
    final res = await query.order('created_at', ascending: false);
    return res.map((e) => BrainDump.fromJson(e)).toList();
  }

  Future<BrainDump> create(BrainDump dump) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('brain_dumps')
        .insert({...dump.toMap(), 'user_id': uid})
        .select()
        .single();
    return BrainDump.fromJson(res);
  }

  Future<void> update(int id, BrainDump dump) async {
    await supabase.from('brain_dumps').update(dump.toMap()).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('brain_dumps').delete().eq('id', id);
  }

  Future<void> togglePin(int id, bool value) async {
    await supabase.from('brain_dumps').update({'is_pinned': value}).eq('id', id);
  }

  Future<void> archive(int id, bool value) async {
    await supabase.from('brain_dumps').update({'is_archived': value}).eq('id', id);
  }
}