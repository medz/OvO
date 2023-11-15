import '../base.dart';
import '../schema.dart';

extension OvoString on OvO {
  OvoSchema<String> string({String? message}) => OvoSchema.fromType(message);
}
