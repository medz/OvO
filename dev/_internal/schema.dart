import '../core/ovo.dart';

class Schema<T> implements OvO<T> {
  Schema();

  // final validators =

  @override
  Future<T> parse(value) async {
    return switch (await Future.value(value)) {
      T value => value,
      _ => throw 'Invalid value',
    };
  }

  static Schema<T> of<T>(OvO<T> schema) {
    if (schema is Schema<T>) {
      return schema;
    }

    return Schema<T>();
  }
}
