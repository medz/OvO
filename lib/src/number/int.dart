import '../base.dart';
import '../parser.dart';
import '../schema.dart';
import '../transform.dart';

typedef OvoIntTypeDef = int;

extension OvoInt on OvO {
  OvoSchema<OvoIntTypeDef> int({String? message}) =>
      OvoSchema.fromType(message);
}

extension OvoIntSchema on OvoSchema<num> {
  OvoSchema<OvoIntTypeDef> int({String? message}) {
    if (this is OvoSchema<OvoIntTypeDef>) {
      return this as OvoSchema<OvoIntTypeDef>;
    }

    return transform((context, data) {
      return switch (data) {
        OvoIntTypeDef value => context.ok(value),
        _ => context.fail(message ?? 'Must be an integer'),
      };
    });
  }
}
