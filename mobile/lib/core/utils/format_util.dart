import 'package:intl/intl.dart';

/// Formatting helpers mirroring the Laravel web app.
abstract class FormatUtil {
  static final NumberFormat _rupiah =
      NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  static String rupiah(num? value) => _rupiah.format(value ?? 0);

  static String percent(num? value) =>
      '${(value ?? 0).toStringAsFixed(1).replaceAll(RegExp(r'\.0$'), '')}%';

  static String date(DateTime? value) =>
      value == null ? '-' : DateFormat('d MMM yyyy', 'id_ID').format(value);

  static String dateTime(DateTime? value) => value == null
      ? '-'
      : DateFormat('d MMM yyyy HH:mm', 'id_ID').format(value);

  static String relative(DateTime? value) {
    if (value == null) return '-';
    final diff = DateTime.now().difference(value);
    if (diff.inSeconds < 60) return 'baru saja';
    if (diff.inMinutes < 60) return '${diff.inMinutes} menit lalu';
    if (diff.inHours < 24) return '${diff.inHours} jam lalu';
    if (diff.inDays < 7) return '${diff.inDays} hari lalu';
    return date(value);
  }
}
