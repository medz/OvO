import '../../ovo.dart';
import '../../schema.dart';

extension OvoStringExtension on OvO {
  OvoSchema<String> string({String? message}) => OvoSchema.fromType(message);
}
