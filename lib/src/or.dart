import 'base.dart';
import 'context.dart';
import 'parser.dart';
import 'schema.dart';

extension OvoOr on OvO {
  OvoSchema or(Iterable<OvoSchema> schemas, {String? message}) {
    final parsers = schemas.map((e) => OvoSchema.parserOf(e));

    return OvoSchema(_OrParser(parsers, message));
  }
}

class _OrParser implements OvoParser {
  final Iterable<OvoParser> parsers;
  final String? message;

  const _OrParser(this.parsers, this.message);

  @override
  Future handle(OvoContext context) async {
    for (final parser in parsers) {
      try {
        final childContext = context.nest(context.data);
        final result = await parser.handle(childContext);

        if (childContext.passed) {
          context.issuesClear();
          childContext.issuesClear();
          return result;
        }
      } on OvoContext catch (_) {
        continue;
      }
    }

    throw context;
  }
}
