import 'package:meta/meta.dart';
import 'package:ovo/src/error.dart';

import 'context.dart';
import 'parser.dart';

class OvoSchema<T> {
  final OvoParser<T> _parser;

  const OvoSchema(OvoParser<T> parser) : _parser = parser;

  factory OvoSchema.fromType([String? message]) =>
      OvoSchema(OvoParser(message));

  static OvoParser<T> parserOf<T>(OvoSchema<T> schema) => schema._parser;

  @mustCallSuper
  Future<T> parse(
    Object? data, {
    OvoThrowMode throwMode = OvoThrowMode.all,
    String? segment,
  }) async {
    final context = OvoContext(data, throwMode: throwMode, segment: segment);

    try {
      final result = await _parser.handle(context);

      if (context.passed) return result;
      throw OvoException(context);
    } on OvoContext catch (context) {
      throw OvoException(context);
    } on OvoException {
      rethrow;
    } on Error catch (error) {
      throw OvoError(context, error);
    }
  }
}
