import 'context.dart';
import 'parser.dart';
import 'schema.dart';

extension OvoNullable<T> on OvoSchema<T> {
  OvoSchema<T?> nullable() {
    final parent = OvoSchema.parserOf(this);
    final parser = _NullableParser(parent);

    return OvoSchema(parser);
  }
}

class _NullableParser<T> implements OvoParser<T?> {
  final OvoParser<T> parser;

  const _NullableParser(this.parser);

  @override
  Future<OvoParserStatus<T?>> handle(OvoContext context) async {
    if (context.data == null) {
      return context.ok(null);
    }

    return parser.handle(context);
  }
}
