import '../base.dart';
import '../parser.dart';
import '../schema.dart';
import '../transform.dart';

typedef OvoDoubleTypeDef = double;

extension OvoInt on OvO {
  OvoSchema<OvoDoubleTypeDef> double({String? message}) =>
      OvoSchema.fromType(message);
}

extension OvoIntSchema on OvoSchema<num> {
  OvoSchema<OvoDoubleTypeDef> double({String? message}) {
    if (this is OvoSchema<OvoDoubleTypeDef>) {
      return this as OvoSchema<OvoDoubleTypeDef>;
    }

    return transform((context, data) {
      return switch (data) {
        OvoDoubleTypeDef value => value,
        _ => context.throws(message ?? 'Must be an double'),
      };
    });
  }
}
