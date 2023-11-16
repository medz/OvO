import 'dart:async';

import '../core/exception.dart';
import '../core/ovo.dart';
import 'transform.dart';

extension Refine<T> on OvO<T> {
  OvO<T> refine(
    FutureOr<bool> Function(T data) test, {
    required String message,
  }) {
    return transform((context, data, next) async {
      final result = await next(data);
      if (await test(result)) return result;

      throw OvOException(code: #custom, message: message);
    });
  }
}
