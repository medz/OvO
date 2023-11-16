import 'context.dart';
import 'exception.dart';

abstract class OvO<T> {
  const factory OvO([String? message]) = _BaiscOvO<T>;

  Future<T> handle(Context context, data);

  static bool has<T>(Object? data) => data is T;

  static T cast<T>(Object? data, {String? message, String? path}) {
    if (data is T) return data;

    throw OvOException(code: #invalid_type, message: 'Invalid type');
  }
}

extension OvOParser<T> on OvO<T> {
  Future<T> parse([Object? data]) async => handle(Context.root(), data);
}

class _BaiscOvO<T> implements OvO<T> {
  final String? message;

  const _BaiscOvO([this.message]);

  @override
  Future<T> handle(Context context, data) async =>
      OvO.cast<T>(data, message: message, path: context.path);
}
