import 'package:ovo/ovo.dart';
import 'package:test/test.dart';

void main() {
  group('AllOf', () {
    test('should return data when all schemas are valid', () async {
      final schemas = [
        const Const(1),
        const Const(2),
        const Const(3),
      ];
      final allOf = AllOf(schemas);

      final result = await allOf.parse(null);

      expect(result, equals(null));
    });

    test('should throw exception when any schema is invalid', () async {
      final schemas = <OvO>[
        const Const(1),
        const Const(2),
        const ThrowException(OvOException(code: #test, message: 'test')),
      ];
      final allOf = AllOf(schemas);

      expect(() async => await allOf.parse(null),
          throwsA(const TypeMatcher<OvOException>()));
    });

    test('should throw exception with custom message', () async {
      final schemas = <OvO>[
        const Const(1),
        const Const(2),
        const ThrowException(OvOException(code: #test, message: 'test')),
      ];
      final allOf = AllOf(schemas, 'Custom message');

      expect(
          () async => await allOf.parse(null),
          throwsA(const TypeMatcher<OvOException>()
              .having((e) => e.message, 'message', 'Custom message')));
    });
  });
}

class Const<T> implements OvO<T> {
  final T value;

  const Const(this.value);

  @override
  Future<T> handle(Context context, data) async {
    return value;
  }
}

class ThrowException<T> implements OvO<T> {
  final OvOException exception;

  const ThrowException(this.exception);

  @override
  Future<T> handle(Context context, data) async {
    throw exception;
  }
}
