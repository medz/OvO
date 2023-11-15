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
  }) async {
    final context = OvoContext(data, throwMode: throwMode);

    try {
      final status = await _parser.handle(context);

      return status.whenSuccessOr((context) => throw OvoException(context));
    } on OvoException {
      rethrow;
    } on Error catch (error) {
      throw OvoError(context, error);
    }
  }
}
