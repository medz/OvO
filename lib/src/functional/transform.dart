import 'dart:async';

import '../core/context.dart';
import '../core/ovo.dart';

extension OvOTransform<T> on OvO<T> {
  OvO<R> transform<R>(
    FutureOr<R> Function(
      Context context,
      dynamic data,
      Future<T> Function(dynamic data) next,
    ) transform,
  ) =>
      _Transform(this, transform);
}

class _Transform<T, R> implements OvO<R> {
  final OvO<T> parent;
  final FutureOr<R> Function(
    Context context,
    dynamic data,
    Future<T> Function(dynamic data) next,
  ) transform;

  const _Transform(this.parent, this.transform);

  @override
  Future<R> handle(Context context, data) async =>
      transform(context, data, (data) => parent.handle(context, data));
}
