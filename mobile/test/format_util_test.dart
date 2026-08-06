import 'package:flutter_test/flutter_test.dart';
import 'package:uro_admin/core/utils/format_util.dart';

void main() {
  group('FormatUtil', () {
    test('rupiah formats Indonesian currency', () {
      expect(FormatUtil.rupiah(1500000), contains('1.500.000'));
      expect(FormatUtil.rupiah(0), contains('0'));
    });

    test('percent trims trailing zeros', () {
      expect(FormatUtil.percent(50), '50%');
      expect(FormatUtil.percent(50.5), '50.5%');
    });

    test('date returns placeholder for null', () {
      expect(FormatUtil.date(null), '-');
      expect(FormatUtil.date(DateTime(2026, 8, 7)), isNot('-'));
    });
  });
}