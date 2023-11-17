import 'package:ovo/ovo.dart';
import 'package:test/test.dart';

void main() {
  group('Nullable', () {
    test('should return null when input is null', () async {
      final nullable = const Const<int>(1).nullable();

      final result = await nullable.parse(null);

      expect(result, isNull);
    });

    test('should return value when input is not null', () async {
      final nullable = const Integer().nullable();

      final result = await nullable.parse(2);

      expect(result, equals(2));
    });

    test('should return null when parent returns null', () async {
      final nullable = const Const<int>(1).nullable();

      final result = await nullable.parse();

      expect(result, isNull);
    });
  });
}
