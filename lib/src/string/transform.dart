import '../parser.dart';
import '../schema.dart';
import '../transform.dart';

extension OvoStringTransform on OvoSchema<String> {
  OvoSchema<String> trim() =>
      transform((context, data) => context.ok(data.trim()));

  OvoSchema<String> trimLeft() =>
      transform((context, data) => context.ok(data.trimLeft()));

  OvoSchema<String> trimRight() =>
      transform((context, data) => context.ok(data.trimRight()));

  OvoSchema<String> toLowerCase() =>
      transform((context, data) => context.ok(data.toLowerCase()));

  OvoSchema<String> toUpperCase() =>
      transform((context, data) => context.ok(data.toUpperCase()));

  OvoSchema<String> replaceAll(Pattern from, String to) =>
      transform((context, data) => context.ok(data.replaceAll(from, to)));

  OvoSchema<String> replaceFirst(Pattern from, String to) =>
      transform((context, data) => context.ok(data.replaceFirst(from, to)));

  OvoSchema<String> padLeft(int width, [String padding = ' ']) =>
      transform((context, data) => context.ok(data.padLeft(width, padding)));

  OvoSchema<String> padRight(int width, [String padding = ' ']) =>
      transform((context, data) => context.ok(data.padRight(width, padding)));
}
