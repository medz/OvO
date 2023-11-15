import 'dart:async';

import 'context.dart';

sealed class OvoParserStatus<T> {
  const factory OvoParserStatus.success(T data) = _OvoParserSuccess<T>;
  const factory OvoParserStatus.failure(OvoContext context) =
      _OvoParserFailure<T>;
}

class _OvoParserSuccess<T> implements OvoParserStatus<T> {
  final T data;

  const _OvoParserSuccess(this.data);
}

class _OvoParserFailure<T> implements OvoParserStatus<T> {
  final OvoContext context;

  const _OvoParserFailure(this.context);
}

extension OvoParserStatusWhen<T> on OvoParserStatus<T> {
  Future<R> when<R>(
    FutureOr<R> Function(T data) success,
    FutureOr<R> Function(OvoContext context) failure,
  ) async {
    return switch (this) {
      _OvoParserSuccess(data: final data) => await success(data),
      _OvoParserFailure(context: final context) => await failure(context),
    };
  }

  Future<T> whenSuccessOr(FutureOr<T> Function(OvoContext context) fail) =>
      when((data) => data, fail);

  Future<OvoParserStatus<R>> cast<R>(
          FutureOr<OvoParserStatus<R>> Function(T data) success) =>
      when(success, (context) => OvoParserStatus.failure(context));
}

extension OvoContextStatusHelper on OvoContext {
  OvoParserStatus<T> ok<T>(T data) => OvoParserStatus.success(data);

  OvoParserStatus<T> fail<T>({required String kind, String? message}) {
    this[kind] = message;

    return OvoParserStatus.failure(this);
  }
}

abstract interface class OvoParser<T> {
  const factory OvoParser([String? message]) = _Parser<T>;

  Future<OvoParserStatus<T>> handle(OvoContext context);
}

final class _Parser<T> implements OvoParser<T> {
  final String? message;

  const _Parser([this.message]);

  @override
  Future<OvoParserStatus<T>> handle(OvoContext context) async {
    return switch (context.data) {
      T data => context.ok(data),
      _ => context.fail(kind: 'invalid_type', message: message),
    };
  }
}
