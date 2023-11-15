import 'base.dart';
import 'schema.dart';

extension OvoRecord on OvO {
  OvoSchema<T> record<T extends Record>({String? message}) =>
      OvoSchema.fromType(message);
}
