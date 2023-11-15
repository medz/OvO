import 'base.dart';
import 'schema.dart';
import 'refine.dart';

extension OvoBigInt on OvO {
  OvoSchema<BigInt> bigint({String? message}) => OvoSchema.fromType(message);
}

extension OvoBigIntSchema on OvoSchema<BigInt> {
  OvoSchema<BigInt> gt(BigInt value, {String? message}) {
    return refine(
      (data) => data > value,
      message: message ?? 'Must be greater than $value',
    );
  }

  OvoSchema<BigInt> gte(BigInt value, {String? message}) {
    return refine(
      (data) => data >= value,
      message: message ?? 'Must be greater than or equal to $value',
    );
  }

  OvoSchema<BigInt> lt(BigInt value, {String? message}) {
    return refine(
      (data) => data < value,
      message: message ?? 'Must be less than $value',
    );
  }

  OvoSchema<BigInt> lte(BigInt value, {String? message}) {
    return refine(
      (data) => data <= value,
      message: message ?? 'Must be less than or equal to $value',
    );
  }

  OvoSchema<BigInt> between(BigInt min, BigInt max, {String? message}) {
    return refine(
      (data) => data >= min && data <= max,
      message: message ?? 'Must be between $min and $max',
    );
  }

  OvoSchema<BigInt> zero(bool value, {String? message}) {
    return refine(
      (data) => (data == BigInt.zero) == value,
      message: message ?? (value ? 'Must be zero' : 'Must not be zero'),
    );
  }
}
