import 'base.dart';
import 'context.dart';
import 'parser.dart';
import 'schema.dart';

extension OvoMap on OvO {
  OvoSchema<Map<K, V>> map<K, V>(OvoSchema<K> key, OvoSchema<V> value,
      {String? message}) {
    return OvoSchema(
      _MapParser(OvoSchema.parserOf(key), OvoSchema.parserOf(value), message),
    );
  }
}

class _MapParser<K, V> implements OvoParser<Map<K, V>> {
  final OvoParser<K> keyParser;
  final OvoParser<V> valueParser;
  final String? message;

  const _MapParser(this.keyParser, this.valueParser, [this.message]);

  @override
  Future<Map<K, V>> handle(OvoContext context) async {
    final Map data = switch (context.data) {
      Map value => value,
      _ => throw context.throws(
          'Invalid type, expected Map but got ${context.data.runtimeType}'),
    };

    final result = <K, V>{};

    for (final (key, value) in data.indexed) {
      final keyContext = context.nest(key, key.toString());
      final valueContext = context.nest(value, key.toString());

      try {
        final parsedKey = await keyParser.handle(keyContext);
        final parsedValue = await valueParser.handle(valueContext);

        result[parsedKey] = parsedValue;
      } on OvoContext catch (context) {
        if (context.throwMode == OvoThrowMode.all) {
          continue;
        }

        rethrow;
      }
    }

    return result;
  }
}

extension<K, V> on Map<K, V> {
  Iterable<(K, V)> get indexed => entries.map((e) => (e.key, e.value));
}
