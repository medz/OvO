import '../core/context.dart';
import '../core/ovo.dart';

class Object implements OvO<Map<String, dynamic>> {
  final Map<String, OvO> properties;
  final String? message;

  const Object(this.properties, [this.message]);

  @override
  Future<Map<String, dynamic>> handle(Context context, data) async {
    final value = OvO.cast<Map<String, dynamic>>(data,
        message: message, path: context.path);
    final result = <String, dynamic>{};
    for (final (key, schema) in properties.indexed) {
      result[key] = await schema.handle(context.child(key), value[key]);
    }

    return result;
  }
}

extension<K, V> on Map<K, V> {
  Iterable<(K, V)> get indexed sync* {
    for (final MapEntry(key: key, value: value) in entries) {
      yield (key, value);
    }
  }
}
