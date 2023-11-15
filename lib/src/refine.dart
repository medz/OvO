import 'dart:async';

import 'parser.dart';
import 'schema.dart';
import 'transform.dart';

extension OvoRefine<T> on OvoSchema<T> {
  OvoSchema<T> refine(
    FutureOr<bool> Function(T data) test, {
    String? message,
  }) {
    return transform<T>((context, data) async {
      return switch (await test(data)) {
        true => context.ok(data),
        false => context.fail(message ?? 'Invalid value'),
      };
    });
  }
}
