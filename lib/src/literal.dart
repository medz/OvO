import 'package:ovo/src/parser.dart';

import 'schema.dart';
import 'transform.dart';

extension OvoLiteral<T> on OvoSchema<T> {
  OvoSchema<T> literal(T value, {String? message}) {
    return transform((context, data) {
      if (data == value) return data;

      return context.throws('Invalid literal, expected $value but got $data');
    });
  }
}
