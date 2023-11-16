import '../core/ovo.dart';
import '../functional/refine.dart';

typedef Boolean = OvO<bool>;

extension BooleanOvO on Boolean {
  Boolean isTrue({String? message}) {
    return refine((data) => data, message: message ?? 'Must be true');
  }

  Boolean isFalse({String? message}) {
    return refine((data) => !data, message: message ?? 'Must be false');
  }
}
