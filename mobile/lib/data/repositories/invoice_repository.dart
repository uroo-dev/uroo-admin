import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/invoice.dart';

class InvoiceRepository {
  Future<List<Invoice>> list() async {
    final res = await supabase
        .from('invoices')
        .select('*, client:clients(name)')
        .order('created_at', ascending: false);
    return res.map((e) => Invoice.fromJson(e)).toList();
  }

  Future<Invoice?> get(int id) async {
    final res = await supabase
        .from('invoices')
        .select('*, client:clients(name)')
        .eq('id', id)
        .maybeSingle();
    return res == null ? null : Invoice.fromJson(res);
  }

  Future<Invoice> create(Invoice invoice) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('invoices')
        .insert({...invoice.toMap(), 'user_id': uid})
        .select()
        .single();
    return Invoice.fromJson(res);
  }

  Future<void> update(int id, Invoice invoice) async {
    await supabase.from('invoices').update(invoice.toMap()).eq('id', id);
  }

  Future<void> markPaid(int id, double paidAmount) async {
    await supabase.from('invoices').update({
      'paid_amount': paidAmount,
      'status': paidAmount >= 0 ? 'lunas' : 'hutang',
    }).eq('id', id);
  }

  Future<void> delete(int id) async {
    await supabase.from('invoices').delete().eq('id', id);
  }
}