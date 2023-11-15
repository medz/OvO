import 'base.dart';
import 'context.dart';
import 'parser.dart';
import 'schema.dart';

extension OvoAnd on OvO {
  OvoSchema<Map> and(Iterable<OvoSchema<Map>> schemas, {String? message}) {
    final parsers = schemas.map((e) => OvoSchema.parserOf(e));

    return OvoSchema(_AndParser(parsers, message));
  }
}

class _AndParser implements OvoParser<Map> {
  final Iterable<OvoParser<Map>> parsers;
  final String? message;

  const _AndParser(this.parsers, this.message);

  @override
  Future<Map> handle(OvoContext context) async {
    final result = <dynamic, dynamic>{};

    for (final parser in parsers) {
      final data = await parser.handle(context);

      result.addAll(data);
    }
  }
}
