import 'package:ovo/ovo.dart';
import 'package:test/test.dart';

void main() {
  group('If', () {
    test('should return then case when condition is true', () async {
      final condition = const Const<bool>(true);
      final then = const Def<int>(1);
      final ifCase = If<int>(condition, then: then);

      final result = await ifCase.parse(true);

      expect(result, equals(1));
    });

    test('should return else case when condition is false', () async {
      final condition = const Const<bool>(false);
      final then = const Def<int>(1);
      final orElse = const Def<int>(2);
      final ifCase = If<int>(condition, then: then, orElse: orElse);

      final result = await ifCase.parse(true);

      expect(result, equals(2));
    });

    test('should throw exception when there is no else case', () async {
      final condition = const Const<bool>(false);
      final then = const Const<int>(1);
      final ifCase = If<int>(condition, then: then);

      expect(() async => await ifCase.parse(null),
          throwsA(const TypeMatcher<OvOException>()));
    });

    test('should throw exception when condition throws exception', () async {
      final condition = const ThrowException<bool>(
          OvOException(code: #test, message: 'test'));
      final then = const Const<int>(1);
      final orElse = const Const<int>(2);
      final ifCase =
          If<int>(condition, then: then, orElse: orElse, message: 'test');

      expect(() async => await ifCase.parse(null),
          throwsA(const TypeMatcher<OvOException>()));
    });
  });
}

class Def<T> implements OvO<T> {
  final T value;

  const Def(this.value);

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
