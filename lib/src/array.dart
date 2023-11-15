import 'dart:async';

import 'base.dart';
import 'context.dart';
import 'parser.dart';
import 'refine.dart';
import 'schema.dart';

extension OvoArray on OvO {
  OvoSchema<Iterable<T>> array<T>(OvoSchema<T> schema, {String? message}) {
    return OvoSchema(
      _ArrayParser(schema, OvoParser<Iterable>(message)),
    );
  }
}

extension OvoArraySchema<T> on OvoSchema<Iterable<T>> {
  OvoSchema<Iterable<T>> min(int length, {String? message}) {
    return refine(
      (data) => data.length >= length,
      message: message ?? 'Array must have at least $length items',
    );
  }

  OvoSchema<Iterable<T>> max(int length, {String? message}) {
    return refine(
      (data) => data.length <= length,
      message: message ?? 'Array must have at most $length items',
    );
  }

  OvoSchema<Iterable<T>> length(int length, {String? message}) {
    return refine(
      (data) => data.length == length,
      message: message ?? 'Array must have exactly $length items',
    );
  }

  OvoSchema<Iterable<T>> isNotEmpty({String? message}) {
    return refine(
      (data) => data.isNotEmpty,
      message: message ?? 'Array must not be empty',
    );
  }
}

class _ArrayParser<T> implements OvoParser<Iterable<T>> {
  final OvoSchema<T> schema;
  final OvoParser<Iterable> parent;

  const _ArrayParser(this.schema, this.parent);

  @override
  Future<Iterable<T>> handle(OvoContext self) async {
    final data = await parent.handle(self);
    final parser = OvoSchema.parserOf(schema);
    final result = <T>[];

    for (final (segment, value) in data.indexed) {
      final context = self.nest(value, segment.toString());

      try {
        result.add(await parser.handle(context));
      } on OvoContext catch (context) {
        if (context.throwMode == OvoThrowMode.first) {
          rethrow;
        }

        continue;
      }
    }

    return result;
  }
}
