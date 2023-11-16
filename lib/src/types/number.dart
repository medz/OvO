import '../core/ovo.dart';
import '../functional/refine.dart';

typedef Number<T extends num> = OvO<T>;
typedef Integer = Number<int>;
typedef Double = Number<double>;

extension NumberOvO<T extends num> on Number<T> {
  Number<T> gt(T value, {String? message}) {
    return refine(
      (data) => data > value,
      message: message ?? 'Must be greater than $value',
    );
  }

  Number<T> gte(T value, {String? message}) {
    return refine(
      (data) => data >= value,
      message: message ?? 'Must be greater than or equal to $value',
    );
  }

  Number<T> lt(T value, {String? message}) {
    return refine(
      (data) => data < value,
      message: message ?? 'Must be less than $value',
    );
  }

  Number<T> lte(T value, {String? message}) {
    return refine(
      (data) => data <= value,
      message: message ?? 'Must be less than or equal to $value',
    );
  }

  Number<T> finite({String? message}) {
    return refine(
      (data) => data.isFinite,
      message: message ?? 'Must be finite',
    );
  }

  Number<T> negative({String? message}) {
    return refine(
      (data) => data.isNegative,
      message: message ?? 'Must be negative',
    );
  }
}
