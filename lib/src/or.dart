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
  Future handle(OvoContext context) {
    final issues = <OvoIssue>[];

    for (final parser in parsers) {
      final tempContext =
          OvoContext(context.data, throwMode: context.throwMode);

      try {
        final result = parser.handle(tempContext);

        if (tempContext.passed) {
          return result;
        }
      } on OvoContext catch (e) {
        issues.addAll(e);
        if (context.throwMode == OvoThrowMode.all) {
          continue;
        }

        break;
      }
    }

    for (final issue in issues) {
      context + issue;
    }

    throw context.throws(message ?? 'No schema matched.');
  }
}
