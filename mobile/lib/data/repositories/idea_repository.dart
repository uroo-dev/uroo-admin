import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/app_idea.dart';

class IdeaRepository {
  Future<List<AppIdea>> list() async {
    final res = await supabase
        .from('app_ideas')
        .select()
        .order('created_at', ascending: false);
    return res.map((e) => AppIdea.fromJson(e)).toList();
  }

  Future<AppIdea> create(AppIdea idea) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('app_ideas')
        .insert({...idea.toMap(), 'user_id': uid})
        .select()
        .single();
    return AppIdea.fromJson(res);
  }

  Future<void> update(int id, AppIdea idea) async {
    await supabase.from('app_ideas').update(idea.toMap()).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('app_ideas').delete().eq('id', id);
  }
}