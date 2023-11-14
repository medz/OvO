import 'dart:async';

import 'package:meta/meta.dart';
import 'package:ovo/src/exception.dart';
import 'package:ovo/src/type.dart';

class OvoStringDef extends OvoTypeDef {
  final FutureOr<bool> Function(Object? data) test;

  const OvoStringDef({
    required this.test,
    super.message,
  });
}

class OvoString extends OvoType<String, OvoStringDef> {
  const OvoString(super.def);

  // @mustCallSuper
  // @override
  // OvoString min(int length, {String? message}) {
  //   Future<bool> test(Object? data) async {}
  // }

  @override
  Future<String> handle(OvoParseContext context) {}
}

OvoString string({String? message}) {
  final def = OvoStringDef(message: message, test: (data) => data is String);

  return OvoString(def);
}
