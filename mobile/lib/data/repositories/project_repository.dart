import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/project.dart';

class ProjectRepository {
  Future<List<Project>> list() async {
    final res = await supabase
        .from('projects')
        .select('*, client:clients(name)')
        .order('created_at', ascending: false);
    return res.map((e) => Project.fromJson(e)).toList();
  }

  Future<Project> create(Project project) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('projects')
        .insert({...project.toMap(), 'user_id': uid})
        .select()
        .single();
    return Project.fromJson(res);
  }

  Future<void> update(int id, Project project) async {
    await supabase.from('projects').update(project.toMap()).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('projects').delete().eq('id', id);
  }
}