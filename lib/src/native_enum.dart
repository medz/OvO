import 'base.dart';
import 'schema.dart';

extension OvoNativeEnum on OvO {
  OvoSchema<T> nativeEnum<T extends Enum>() => OvoSchema.fromType();
}
