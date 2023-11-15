import 'base.dart';
import 'schema.dart';
import 'refine.dart';

extension OvoDateTime on OvO {
  OvoSchema<DateTime> datetime({String? message}) =>
      OvoSchema.fromType(message);
}

extension OvoDateTimeSchema on OvoSchema<DateTime> {
  OvoSchema<DateTime> gt(DateTime value, {String? message}) {
    return refine(
      (data) => data.isAfter(value),
      message: message ?? 'Must be greater than $value',
    );
  }

  OvoSchema<DateTime> gte(DateTime value, {String? message}) {
    return refine(
      (data) => data.isAfter(value) || data == value,
      message: message ?? 'Must be greater than or equal to $value',
    );
  }

  OvoSchema<DateTime> lt(DateTime value, {String? message}) {
    return refine(
      (data) => data.isBefore(value),
      message: message ?? 'Must be less than $value',
    );
  }

  OvoSchema<DateTime> lte(DateTime value, {String? message}) {
    return refine(
      (data) => data.isBefore(value) || data == value,
      message: message ?? 'Must be less than or equal to $value',
    );
  }

  OvoSchema<DateTime> between(DateTime min, DateTime max, {String? message}) {
    return refine(
      (data) => data.isAfter(min) && data.isBefore(max),
      message: message ?? 'Must be between $min and $max',
    );
  }

  OvoSchema<DateTime> utc({String? message}) {
    return refine(
      (data) => data.isUtc,
      message: message ?? 'Must be UTC',
    );
  }

  OvoSchema<DateTime> local({String? message}) {
    return refine(
      (data) => !data.isUtc,
      message: message ?? 'Must be local',
    );
  }
}
