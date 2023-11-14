import 'package:ovo/src/exception.dart';

class OvoTypeDef {
  final String? message;

  const OvoTypeDef({this.message});
}

sealed class OvoParseResult<T> {}

class OvoParseSuccess<T> implements OvoParseResult<T> {
  final T value;

  const OvoParseSuccess(this.value);
}

class OvoParseFailure<T> implements OvoParseResult<T> {
  final List<OvoIssue> issues;

  const OvoParseFailure(this.issues);
}

class OvoParseContext {
  final Iterable<String> path;
  final OvoMessageMapper? mapper;
  final dynamic data;
  final OvoParseContext? parent;

  const OvoParseContext({
    required this.path,
    required this.data,
    this.mapper,
    this.parent,
  });
}

abstract class OvoType<T, Def extends OvoTypeDef> {
  final Def def;

  const OvoType(this.def);

  Future<T> handle(OvoParseContext context);

  Future<OvoParseResult<T>> safeParse(
    Object? data, {
    OvoMessageMapper? mapper,
    Iterable<String> path = const <String>[],
  }) async {
    try {
      final result = await parse(data, mapper: mapper, path: path);

      return OvoParseSuccess(result);
    } on OvoException catch (e) {
      return OvoParseFailure(e.issues);
    }
  }

  Future<T> parse(
    Object? data, {
    OvoMessageMapper? mapper,
    Iterable<String> path = const <String>[],
  }) async {
    final context = OvoParseContext(path: path, data: data, mapper: mapper);

    try {
      return await handle(context);
    } on OvoException {
      rethrow;
    } on Exception catch (e) {
      final exceptionContext = OvoExceptionContext(data, e.toString());
      final message = mapper?.call(
            OvoIssueCode.custom,
            exceptionContext,
          ) ??
          exceptionContext.message;
      final issue = OvoIssue(OvoIssueCode.custom, message: message);

      throw OvoException<T>([issue]);
    } on Error catch (e) {
      final exceptionContext = OvoExceptionContext(data, Error.safeToString(e));
      final message = mapper?.call(
            OvoIssueCode.custom,
            exceptionContext,
          ) ??
          exceptionContext.message;
      final issue = OvoIssue(OvoIssueCode.custom, message: message);

      throw OvoException<T>([issue]);
    }
  }
}
