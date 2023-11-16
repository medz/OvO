import '../core/context.dart';
import '../core/ovo.dart';
import '../functional/refine.dart';

class Array<T> implements OvO<Iterable<T>> {
  final OvO<T> schema;
  final String? message;

  const Array(this.schema, [this.message]);

  @override
  Future<Iterable<T>> handle(Context context, data) async {
    final iterable =
        OvO.cast<Iterable>(data, message: message, path: context.path);
    final result = <T>[];

    for (final (index, value) in iterable.indexed) {
      final childContext = context.child(index.toString());
      final childValue = await schema.handle(childContext, value);

      result.insert(index, childValue);
    }

    return result;
  }
}

extension ArrayOvO<T> on OvO<Iterable<T>> {
  OvO<Iterable<T>> min(int length, {String? message}) {
    return refine((data) => data.length >= length,
        message: message ?? 'Must have at least $length items');
  }

  OvO<Iterable<T>> max(int length, {String? message}) {
    return refine((data) => data.length <= length,
        message: message ?? 'Must have at most $length items');
  }

  OvO<Iterable<T>> size(int length, {String? message}) {
    return refine((data) => data.length == length,
        message: message ?? 'Must have exactly $length items');
  }

  OvO<Iterable<T>> unique({String? message}) {
    return refine((data) => data.toSet().length == data.length,
        message: message ?? 'Must have unique items');
  }
}
