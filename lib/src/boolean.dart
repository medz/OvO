import 'base.dart';
import 'schema.dart';
import 'refine.dart';

extension OvoBoolean on OvO {
  OvoSchema<bool> boolean({String? message}) => OvoSchema.fromType(message);
}

extension OvoBooleanSchema on OvoSchema<bool> {
  OvoSchema<bool> equals(bool value, {String? message}) {
    return refine(
      (data) => data == value,
      message: message ?? 'Must be equal to $value',
    );
  }

  OvoSchema<bool> not(bool value, {String? message}) {
    return refine(
      (data) => data != value,
      message: message ?? 'Must not be equal to $value',
    );
  }

  OvoSchema<bool> isTrue({String? message}) => equals(true, message: message);
  OvoSchema<bool> isFalse({String? message}) => equals(false, message: message);
}
