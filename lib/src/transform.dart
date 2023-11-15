import 'dart:async';

import 'context.dart';
import 'parser.dart';
import 'schema.dart';

extension OvoTransform<T> on OvoSchema<T> {
  OvoSchema<R> transform<R>(
      FutureOr<R> Function(OvoContext context, T data) transform) {
    final parent = OvoSchema.parserOf(this);
    final parser = _TransformParser(parent, transform);

    return OvoSchema(parser);
  }
}

class _TransformParser<T, R> implements OvoParser<R> {
  final OvoParser<T> parser;
  final FutureOr<R> Function(OvoContext, T) transform;

  const _TransformParser(this.parser, this.transform);

  @override
  Future<R> handle(OvoContext context) async {
    final data = await parser.handle(context);

    return await transform(context, data);
  }
}
