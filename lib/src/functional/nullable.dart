import '../core/context.dart';
import '../core/ovo.dart';

extension Nullable<T> on OvO<T> {
  OvO<T?> nullable() => _Nullable(this);
}

class _Nullable<T> implements OvO<T?> {
  final OvO<T> parent;

  const _Nullable(this.parent);

  @override
  Future<T?> handle(Context context, data) async {
    if (data == null) return null;

    return parent.handle(context, data);
  }
}
