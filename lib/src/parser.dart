import 'dart:async';

import 'context.dart';

extension OvoContextStatusHelper on OvoContext {
  Never throws(String message) => throw (this + OvoIssue(message));
}

abstract interface class OvoParser<T> {
  const factory OvoParser([String? message]) = _Parser<T>;

  Future<T> handle(OvoContext context);
}

final class _Parser<T> implements OvoParser<T> {
  final String? message;

  const _Parser([this.message]);

  @override
  Future<T> handle(OvoContext context) async {
    return switch (context.data) {
      T data => data,
      _ => context.throws(
          'Invalid type, expected $T but got ${context.data.runtimeType}'),
    };
  }
}
