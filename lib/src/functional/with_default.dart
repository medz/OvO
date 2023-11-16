import '../core/ovo.dart';
import 'transform.dart';

extension WithDefault<T extends Object> on OvO<T> {
  OvO<T> withDefault(T defaultValue) {
    return transform((context, data, next) async {
      return next(data ?? defaultValue);
    });
  }
}
