import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/note.dart';

class NoteRepository {
  Future<List<Note>> list({bool pinnedOnly = false}) async {
    var query = supabase
        .from('notes')
        .select()
        .order('created_at', ascending: false);
    if (pinnedOnly) query = query.eq('is_pinned', true);
    final res = await query;
    return res.map((e) => Note.fromJson(e)).toList();
  }

  Future<Note> create(Note note) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('notes')
        .insert({...note.toMap(), 'user_id': uid})
        .select()
        .single();
    return Note.fromJson(res);
  }

  Future<void> update(int id, Note note) async {
    await supabase.from('notes').update(note.toMap()).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('notes').delete().eq('id', id);
  }

  Future<void> togglePin(int id, bool value) async {
    await supabase.from('notes').update({'is_pinned': value}).eq('id', id);
  }

  Future<void> toggleFavorite(int id, bool value) async {
    await supabase.from('notes').update({'is_favorite': value}).eq('id', id);
  }
}