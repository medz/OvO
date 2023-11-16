import '../core/context.dart';
import '../core/ovo.dart';

class Object<T> implements OvO<Map<String, T>> {
  final Map<String, OvO<T>> properties;
  final String? message;

  const Object(this.properties, [this.message]);

  @override
  Future<Map<String, T>> handle(Context context, data) async {
    final value = OvO.cast<Map<String, dynamic>>(data,
        message: message, path: context.path);
    final result = <String, T>{};
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
