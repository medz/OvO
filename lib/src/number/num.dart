import '../base.dart';
import '../schema.dart';
import '../refine.dart';

extension OvoNum on OvO {
  OvoSchema<num> number({String? message}) => OvoSchema.fromType(message);
}

extension OvoNumSchema<T extends num> on OvoSchema<T> {
  OvoSchema<T> gt(num value, {String? message}) {
    return refine(
      (data) => data > value,
      message: message ?? 'Must be greater than $value',
    );
  }

  OvoSchema<T> gte(num value, {String? message}) {
    return refine(
      (data) => data >= value,
      message: message ?? 'Must be greater than or equal to $value',
    );
  }

  OvoSchema<T> lt(num value, {String? message}) {
    return refine(
      (data) => data < value,
      message: message ?? 'Must be less than $value',
    );
  }

  OvoSchema<T> lte(num value, {String? message}) {
    return refine(
      (data) => data <= value,
      message: message ?? 'Must be less than or equal to $value',
    );
  }

  OvoSchema<T> between(num min, num max, {String? message}) {
    return refine(
      (data) => data >= min && data <= max,
      message: message ?? 'Must be between $min and $max',
    );
  }

  // ignore: non_constant_identifier_names
  OvoSchema<T> NaN(bool value, {String? message}) {
    return refine(
      (data) => data.isNaN == value,
      message: message ?? (value ? 'Must be NaN' : 'Must not be NaN'),
    );
  }

  OvoSchema<T> finite(bool value, {String? message}) {
    return refine(
      (data) => data.isFinite == value,
      message: message ?? (value ? 'Must be finite' : 'Must not be finite'),
    );
  }

  OvoSchema<T> infinite(bool value, {String? message}) {
    return refine(
      (data) => data.isInfinite == value,
      message: message ?? (value ? 'Must be infinite' : 'Must not be infinite'),
    );
  }
}
